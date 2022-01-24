import React from "react";
import styled from "styled-components";
import OverviewImage from "../Assets/images/overviewWeb.png";
import { FaGooglePlay, FaApple } from "react-icons/fa";
import { colors } from '../config/colors'

const Overview = () => {
  return (
    <Container>
      <Content>
        <Left>
          <Span>You Found It Eureka Got You.</Span>
          <br />
          <Mobile>
            <MobileImage src={OverviewImage} />
          </Mobile>
          <br />
          <Discription>
            A platform built to connect businesses and customers. Making buying
            and selling interesting
          </Discription>
          <ButtonContainer>
            <Button>
              <FaGooglePlay size="2rem" />
              <Text>Download on playstore</Text>
            </Button>
            <Button>
              <FaApple size="2rem" />
              <Text>Download on Appstore</Text>
            </Button>
          </ButtonContainer>
        </Left>
        <Right>
          <Image src={OverviewImage} />
        </Right>
      </Content>
    </Container>
  );
};

export default Overview;

const Container = styled.div`
  height: 100vh;
  width: 100%;
  background-color: ${colors.primary};
  margin-top: 0;
  display: flex;
  justify-content: center;
  align-items: center;

  @media screen and (max-width: 760px) {
    height: 95vh;
  }
`;

const Content = styled.div`
  height: 80%;
  width: 70%;
  display: flex;
  justify-content: space-between;
  align-items: center;
`;

const Left = styled.div`
  height: 100%;
  width: 48%;
  text-align: right;

  @media screen and (max-width: 760px) {
    width: 95%;
  }
`;

const Mobile = styled.div`
  height: 13rem;
  width: 100%;
  display: none;
  overflow: hidden;
  text-align: center;

  @media screen and (max-width: 760px) {
    display: block;
  }
`;

const MobileImage = styled.img`
  height: 20rem;
  width: 100%;
`;

const Span = styled.span`
  font-size: 4rem;
  font-weight: bold;
  line-height: 4.5rem;

  @media screen and (max-width: 760px) {
    font-size: 2.6rem;
    font-weight: bold;
    line-height: 2rem;
  }
`;

const Discription = styled.span`
  font-size: 1.5rem;
  line-height: 2.5rem;

  @media screen and (max-width: 760px) {
    font-size: 1.3rem;
    font-weight: 500;
    line-height: 2rem;
    text-align: center;
  }
`;

const ButtonContainer = styled.div`
  display: flex;
  flex-direction: row-reverse;
  margin-top: 1.5rem;

  @media screen and (max-width: 760px) {
    align-items: center;
    justify-content: center;
  }
`;

const Button = styled.div`
  width: 9rem;
  height: 2.5rem;
  border-radius: 0.8rem;
  border: 1px solid  ${colors.white};
  background-color:  ${colors.black};
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 2rem;
  padding: 0.5rem;

`;

// const IconApple = styled(FaApple)`
//   font-size: 2rem;
//   margin: 0;
// `;

const Text = styled.h4`
  font-size: 0.9rem;
  margin: 1rem;
  font-weight: 500;
  text-align: left;
`;

const Right = styled.div`
  height: 100%;
  width: 48%;

  @media screen and (max-width: 760px) {
    display: none;
  }
`;

const Image = styled.img`
  height: 100%;
  width: 100%;
`;
